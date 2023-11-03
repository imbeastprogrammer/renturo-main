import { useState } from 'react';
import {
    Bar,
    BarChart,
    Cell,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    TooltipProps,
} from 'recharts';
import {
    ValueType,
    NameType,
} from 'recharts/types/component/DefaultTooltipContent';
import formatCurrency from '@/lib/formatCurrency';

const data = [
    {
        name: 'Jan',
        total: Math.floor(Math.random() * 5000) + 1000,
    },
    {
        name: 'Feb',
        total: Math.floor(Math.random() * 5000) + 1000,
    },
    {
        name: 'Mar',
        total: Math.floor(Math.random() * 5000) + 1000,
    },
    {
        name: 'Apr',
        total: Math.floor(Math.random() * 5000) + 1000,
    },
    {
        name: 'May',
        total: Math.floor(Math.random() * 5000) + 1000,
    },
    {
        name: 'Jun',
        total: Math.floor(Math.random() * 5000) + 1000,
    },
    {
        name: 'Jul',
        total: Math.floor(Math.random() * 5000) + 1000,
    },
    {
        name: 'Aug',
        total: Math.floor(Math.random() * 5000) + 1000,
    },
    {
        name: 'Sep',
        total: Math.floor(Math.random() * 5000) + 1000,
    },
    {
        name: 'Oct',
        total: Math.floor(Math.random() * 5000) + 1000,
    },
    {
        name: 'Nov',
        total: Math.floor(Math.random() * 5000) + 1000,
    },
    {
        name: 'Dec',
        total: Math.floor(Math.random() * 5000) + 1000,
    },
];

const CustomTooltip = ({
    active,
    payload,
}: TooltipProps<ValueType, NameType>) => {
    if (active && payload && payload.length) {
        return (
            <div className='rounded-lg bg-black p-2 px-4 text-white'>
                {payload?.[0]?.value &&
                    formatCurrency(Number(payload[0].value))}
            </div>
        );
    }

    return null;
};

function MontlyRevenue() {
    const [focusBar, setFocusBar] = useState<number | null>(null);

    return (
        <div className='grid h-full w-full grid-rows-[auto_1fr] gap-2 rounded-lg border p-4 shadow-lg'>
            <div>
                <h1 className='text-[22px] font-semibold leading-none'>
                    Montly Revenue
                </h1>
                <span className='text-[30px] font-semibold'>
                    {formatCurrency(48_000)}
                </span>
            </div>
            <ResponsiveContainer width='100%' height='100%'>
                <BarChart
                    data={data}
                    onMouseMove={(state) => {
                        if (state.isTooltipActive)
                            setFocusBar(state.activeTooltipIndex!);
                        else setFocusBar(null);
                    }}
                >
                    <XAxis
                        dataKey='name'
                        stroke='#888888'
                        fontSize={12}
                        tickLine={false}
                        axisLine={false}
                    />
                    <Tooltip content={<CustomTooltip />} cursor={false} />
                    <Bar dataKey='total' fill='#F0F0F0' radius={[4, 4, 4, 4]}>
                        {data.map((_, i) => (
                            <Cell
                                key={i}
                                fill={focusBar === i ? '#EDCA5E' : '#F0F0F0'}
                            />
                        ))}
                    </Bar>
                </BarChart>
            </ResponsiveContainer>
        </div>
    );
}

export default MontlyRevenue;
