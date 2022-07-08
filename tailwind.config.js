/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./src/templates/**/*.php"],
  theme: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/line-clamp'),
    require('@tailwindcss/aspect-ratio'),
  ],
}
