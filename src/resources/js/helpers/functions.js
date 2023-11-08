export function numericFormat(number) {
    const parts = parseFloat(number).toFixed(2).toString().split(".");
    const formattedNumber = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".") + "." + parts[1];
    return formattedNumber;
}
