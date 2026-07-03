/** @type {import('tailwindcss').Config} */
export default {
  prefix: 'tw-',
  corePlugins: {
    preflight: false,
  },
  content: ['./index.html', './admin.html', './src/**/*.{js,jsx}', './*.jsx'],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
