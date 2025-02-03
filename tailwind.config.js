const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
      './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
      './storage/framework/views/*.php',
      './resources/views/**/*.blade.php',
    ],
    safelist: [
      'low-stock-card',
      'low-stock-row',
      'bg-yellow-50',
      'text-yellow-800',
      'border-yellow-500',
      'pulseWarningBg',
      'rowPulse',
      'borderPulse',
    ],
    important: true,
    theme: {
      extend: {
        fontFamily: {
          sans: ['Figtree', ...defaultTheme.fontFamily.sans],
        },
        transitionProperty: {
          'width': 'width',
          'margin': 'margin',
        },
        animation: {
          pulseWarningBg: 'pulseWarningBg 1.5s infinite',
          rowPulse: 'rowPulse 2s infinite',
          borderPulse: 'borderPulse 1.5s infinite',
        },
        keyframes: {
          pulseWarningBg: {
            '0%, 100%': { backgroundColor: '#fefce8' }, // yellow-50
            '50%': { backgroundColor: '#fef9c3' }, // yellow-100
          },
          rowPulse: {
            '0%, 100%': { backgroundColor: '#fefce8' }, // yellow-50
            '50%': { backgroundColor: '#fef9c3' }, // yellow-100
          },
          borderPulse: {
            '0%, 100%': { opacity: '1', transform: 'scaleY(1)' },
            '50%': { opacity: '0.8', transform: 'scaleY(0.95)' },
          },
        },
      },
    },
    plugins: [require('@tailwindcss/forms')],
  };
