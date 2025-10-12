import { ReactNode } from 'react';
import OwnerLayout from '@/layouts/OwnerLayout';

function Calendar() {
    return <div>Calendar</div>;
}

Calendar.layout = (page: ReactNode) => <OwnerLayout>{page}</OwnerLayout>;

export default Calendar;
