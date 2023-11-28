import { format, isDate } from 'date-fns';

const formatDate = (date: string | null, formatOption?: string) => {
    if (!date) return 'NA';

    const dateToFormat = new Date(date);
    if (!isDate(dateToFormat)) throw new Error('Invalid date format');

    return format(dateToFormat, (formatOption = 'MMM dd yyyy'));
};

export default formatDate;
