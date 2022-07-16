/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./index.html",
        "./resources/**/*.{vue,js,ts,jsx,tsx}",
    ],
    safelist: [
        {
            pattern: /(bg|text)-(red|green|amber|orange)-(300|600)/,
        },
    ],
    theme: {
        extend: {},
    },
    plugins: [],
}
