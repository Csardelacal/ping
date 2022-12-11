module.exports = {
  darkMode: 'class',
  content: [
    './bin/templates/**/*.php',
    './bin/error_pages/**/*.php',
    './assets/src/components/**/*.vue',
    './assets/src/pages/**/*.vue',
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography')
  ],
}
