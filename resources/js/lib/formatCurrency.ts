const formatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'PHP',
    maximumFractionDigits: 0,
});

const formatCurrency = (value: number) => {
    if (!value) return formatter.format(0);
    if (typeof value !== 'number') return 'The value should be typeof number';
    return formatter.format(value);
};

export default formatCurrency;
