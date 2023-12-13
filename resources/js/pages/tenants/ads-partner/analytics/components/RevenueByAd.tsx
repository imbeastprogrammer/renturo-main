import { PieChart, Pie, Cell, ResponsiveContainer } from 'recharts';

const data = [
    { name: 'Group A', value: 400 },
    { name: 'Group B', value: 300 },
    { name: 'Group C', value: 300 },
    { name: 'Group D', value: 200 },
];
const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042'];

function RevenueByAd() {
    return (
        <div className='grid h-full grid-rows-[auto_1fr] gap-4 rounded-lg border bg-white p-4 shadow-lg'>
            <div>
                <h1 className='text-lg'>Revenue By Ad</h1>
            </div>
            <div className='grid grid-cols-2 items-center gap-4'>
                <div className='relative h-full w-full'>
                    <ResponsiveContainer width='100%' height='100%'>
                        <PieChart>
                            <Pie
                                data={data}
                                cx='50%'
                                cy='50%'
                                innerRadius={60}
                                outerRadius={80}
                                fill='#8884d8'
                                dataKey='value'
                            >
                                {data.map((entry, index) => (
                                    <Cell
                                        key={`cell-${index}`}
                                        fill={COLORS[index % COLORS.length]}
                                    />
                                ))}
                            </Pie>
                        </PieChart>
                    </ResponsiveContainer>
                    <div className='absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-[25px] font-semibold'>
                        ₱ 50.1K
                    </div>
                </div>
                <div className='space-y-2'>
                    {data.map((entry, index) => (
                        <Legend
                            color={COLORS[index % COLORS.length]}
                            key={index}
                            title={`${entry.name} (50%)`}
                            description='[Ad name]'
                            revenue='₱ 15,400'
                        />
                    ))}
                </div>
            </div>
        </div>
    );
}

type LegendProps = {
    color: string;
    title: string;
    description: string;
    revenue: string;
};
function Legend({ color, title, description, revenue }: LegendProps) {
    return (
        <div className='flex justify-between gap-4'>
            <div className='flex items-start gap-4'>
                <div
                    className='h-[20px] w-[20px] rounded-full border-2'
                    style={{ borderColor: color }}
                />
                <div>
                    <h2 className='text-sm leading-none'>{title}</h2>
                    <span className='text-[10px] text-black/60'>
                        {description}
                    </span>
                </div>
            </div>
            <p className='text-sm'>{revenue}</p>
        </div>
    );
}

export default RevenueByAd;
