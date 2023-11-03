import {
    Cell,
    Pie,
    PieChart,
    ResponsiveContainer,
    Tooltip,
    TooltipProps,
} from 'recharts';
import {
    ValueType,
    NameType,
} from 'recharts/types/component/DefaultTooltipContent';

const data = [
    {
        name: 'Group A',
        value: 50,
    },
    {
        name: 'Group B',
        value: 10,
    },
    {
        name: 'Group C',
        value: 40,
    },
];

const COLORS = ['#185ADC', '#EDCA5E', '#DC8A4A'];
const ColorsInRGBA = ['#f7faff', '#fffdf9', '#fffaf7'];

const CustomTooltip = ({
    active,
    payload,
    label,
}: TooltipProps<ValueType, NameType>) => {
    if (active && payload && payload.length) {
        return (
            <div className='rounded-lg bg-black p-2 px-4 text-white'>
                {`${payload?.[0]?.name}: ${payload?.[0]?.value}`}
            </div>
        );
    }

    return null;
};

function MontlyTrends() {
    return (
        <div className='grid h-full w-full grid-rows-[auto_1fr_auto] gap-4 rounded-lg border p-4 shadow-lg'>
            <div>
                <h1 className='text-[22px] font-semibold'>Monthly Trends</h1>
            </div>
            <ResponsiveContainer width='100%' height='100%'>
                <PieChart>
                    <Pie
                        data={data}
                        cx='50%'
                        cy='50%'
                        labelLine={false}
                        outerRadius={70}
                        dataKey='value'
                    >
                        {data.map((_, index) => (
                            <Cell
                                key={`cell-${index}`}
                                fill={COLORS[index % COLORS.length]}
                                style={{ outline: 'none' }}
                            />
                        ))}
                    </Pie>
                    <Pie
                        data={data}
                        cx='50%'
                        cy='50%'
                        labelLine={false}
                        outerRadius={60}
                        dataKey='value'
                    >
                        {data.map((_, i) => (
                            <Cell
                                key={`cell-${i}`}
                                style={{ outline: 'none' }}
                                fill={ColorsInRGBA[i % ColorsInRGBA.length]}
                            />
                        ))}
                    </Pie>
                    <Tooltip content={<CustomTooltip />} />
                </PieChart>
            </ResponsiveContainer>
            <div className='flex items-center justify-center gap-2'>
                {data.map((d, i) => (
                    <div key={i} className='flex items-center gap-2'>
                        <div
                            className='h-4 w-4 rounded-full border-2'
                            style={{ borderColor: COLORS[i % COLORS.length] }}
                        />
                        <span className='text-[15px]'>{d.name}</span>
                    </div>
                ))}
            </div>
        </div>
    );
}

export default MontlyTrends;
