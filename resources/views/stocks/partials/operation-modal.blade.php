@if($selectedDivision !== 'all')
    <div class="modal fade" id="operationModal" tabindex="-1" aria-labelledby="operationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="operationForm" method="POST" action="{{ route('stocks.transactions.storeOutLoanReturn') }}">
                @csrf
                <input type="hidden" name="stock_id" id="stock_id">
                <input type="hidden" name="force_operation_type" id="force_operation_type" value="">
                <div class="modal-content shadow-lg rounded-lg">
                    <div class="modal-header text-white" style="background: linear-gradient(45deg, #4f46e5, #3b82f6);">
                        <h5 class="modal-title" id="operationModalLabel">Confirm Transaction Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="mb-3" id="operationTypeContainer">
                            <label class="form-label fw-bold">Operation Type:</label>
                            <div class="btn-group" role="group" aria-label="Operation Type">
                                <input type="radio" class="btn-check" name="operation_type" id="btn-out" autocomplete="off" value="OUT" checked>
                                <label class="btn btn-out-custom" for="btn-out">OUT</label>
                                <input type="radio" class="btn-check" name="operation_type" id="btn-loan" autocomplete="off" value="LOAN">
                                <label class="btn btn-loan-custom" for="btn-loan">LOAN</label>
                            </div>
                        </div>
                        <div class="mb-3" id="loanFields" style="display: none;">
                            <div class="mb-3">
                                <label for="manual_date" class="form-label fw-bold">Transaction Date</label>
                                <input type="date" name="manual_date" id="manual_date" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="delivery_note" class="form-label fw-bold">Delivery Note</label>
                                <input type="text" name="delivery_note" id="delivery_note" class="form-control" placeholder="Enter delivery note">
                            </div>
                            <div class="mb-3">
                                <label for="document_number" class="form-label fw-bold">Document Number</label>
                                <input type="text" name="document_number" id="document_number" class="form-control" placeholder="Enter document number">
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Enter name" required>
                            </div>
                            <div class="mb-3">
                                <label for="taken_by" class="form-label fw-bold">Taken by</label>
                                <input type="text" name="taken_by" id="taken_by" class="form-control" placeholder="Enter taken by" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="operationRemark" class="form-label fw-bold">Remarks</label>
                            <textarea name="remarks" id="operationRemark" class="form-control" rows="3" placeholder="Enter remarks..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-submit-transaction">Submit Transaction</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif