<?php

namespace App\Http\Controllers;

use App\Models\StockTransaction;
use App\Models\BulkBatch;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

/**
 * This is a debugging controller to help troubleshoot bulk stock loans
 */
class StockDebugController extends Controller
{
    /**
     * Run diagnostic tests on bulk stock loans
     */
    public function diagnoseBulkLoans()
    {
        // Get all bulk transactions marked as LOAN with quantity > 0
        $allBulkLoans = StockTransaction::where('type', 'LOAN')
            ->whereNotNull('bulk_batch_id')
            ->where('quantity', '>', 0)
            ->get();
        
        Log::info("Found {$allBulkLoans->count()} bulk loan transactions");
        
        // Check each transaction for proper references
        $validTransactions = 0;
        $missingBatch = 0;
        $missingProduct = 0;
        $details = [];
        
        foreach ($allBulkLoans as $txn) {
            $txnDetails = [
                'id' => $txn->id,
                'batch_id' => $txn->bulk_batch_id,
                'quantity' => $txn->quantity,
                'date' => $txn->transaction_date->format('Y-m-d H:i:s'),
                'taken_by' => $txn->taken_by,
                'issues' => []
            ];
            
            // Check if batch exists
            $batch = BulkBatch::find($txn->bulk_batch_id);
            if (!$batch) {
                $txnDetails['issues'][] = "Missing batch record";
                $missingBatch++;
                $details[] = $txnDetails;
                continue;
            }
            
            $txnDetails['batch_info'] = [
                'division_id' => $batch->division_id,
                'product_id' => $batch->product_id,
                'remaining_qty' => $batch->remaining_quantity,
                'original_qty' => $batch->original_quantity,
            ];
            
            // Check if product exists
            $product = Product::find($batch->product_id);
            if (!$product) {
                $txnDetails['issues'][] = "Missing product record";
                $missingProduct++;
                $details[] = $txnDetails;
                continue;
            }
            
            $txnDetails['product_info'] = [
                'category' => $product->category,
                'brand' => $product->brand,
                'model' => $product->model,
            ];
            
            // This transaction seems valid
            $validTransactions++;
            
            // Only add to details if there were issues
            if (!empty($txnDetails['issues'])) {
                $details[] = $txnDetails;
            }
        }
        
        // Return diagnostic information
        return response()->json([
            'total_transactions' => $allBulkLoans->count(),
            'valid_transactions' => $validTransactions,
            'missing_batch' => $missingBatch,
            'missing_product' => $missingProduct,
            'problem_transactions' => $details
        ]);
    }
    
    /**
     * Force sync all bulk loans to ensure they appear in the UI
     */
    public function fixBulkLoans()
    {
        try {
            DB::beginTransaction();
            
            // Get all bulk loans
            $allBulkLoans = StockTransaction::where('type', 'LOAN')
                ->whereNotNull('bulk_batch_id')
                ->where('quantity', '>', 0)
                ->get();
                
            Log::info("Attempting to fix {$allBulkLoans->count()} bulk loan transactions");
            
            $fixed = 0;
            $skipped = 0;
            $errors = 0;
            
            foreach ($allBulkLoans as $txn) {
                try {
                    // Get batch and product
                    $batch = BulkBatch::find($txn->bulk_batch_id);
                    if (!$batch) {
                        Log::warning("Skipping transaction {$txn->id} - batch not found");
                        $skipped++;
                        continue;
                    }
                    
                    $product = Product::find($batch->product_id);
                    if (!$product) {
                        Log::warning("Skipping transaction {$txn->id} - product not found");
                        $skipped++;
                        continue;
                    }
                    
                    // Force update essential fields if they're missing
                    $updated = false;
                    
                    if (!$txn->category && $product->category) {
                        $txn->category = $product->category;
                        $updated = true;
                    }
                    
                    if (!$txn->stock_model && $product->model) {
                        $txn->stock_model = $product->model;
                        $updated = true;
                    }
                    
                    if (!$txn->brand && $product->brand) {
                        $txn->brand = $product->brand;
                        $updated = true;
                    }
                    
                    // Save changes if needed
                    if ($updated) {
                        $txn->save();
                        $fixed++;
                    } else {
                        $skipped++;
                    }
                } catch (\Exception $e) {
                    Log::error("Error fixing transaction {$txn->id}: " . $e->getMessage());
                    $errors++;
                }
            }
            
            DB::commit();
            
            return response()->json([
                'total_transactions' => $allBulkLoans->count(),
                'fixed' => $fixed,
                'skipped' => $skipped,
                'errors' => $errors,
                'message' => "Fixed $fixed transactions. Please refresh your page to see the updated loaned items."
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error in fixBulkLoans: " . $e->getMessage());
            
            return response()->json([
                'error' => true,
                'message' => "Error fixing bulk loans: " . $e->getMessage()
            ], 500);
        }
    }
}