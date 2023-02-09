module.exports = {
  darkMode: 'class',
  content: [
    './bin/templates/**/*.php',
    './bin/error_pages/**/*.php',
    './resources/assets/components/**/*.vue',
    './resources/assets/pages/**/*.vue',
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography')
  ],
}
