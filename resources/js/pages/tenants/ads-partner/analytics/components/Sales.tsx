import {
    BarChart,
    Bar,
    Rectangle,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    ResponsiveContainer,
} from 'recharts';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

const data = [
    {
        name: 'Oct. 22',
        previous: 4000,
        current: 2400,
    },
    {
        name: 'Oct. 23',
        previous: 3000,
        current: 1398,
    },
    {
        name: 'Oct. 24',
        previous: 2000,
        current: 9800,
    },
    {
        name: 'Oct. 25',
        previous: 2780,
        current: 3908,
    },
    {
        name: 'Oct. 26',
        previous: 1890,
        current: 4800,
    },
    {
        name: 'Oct. 27',
        previous: 2390,
        current: 3800,
    },
    {
        name: 'Oct. 28',
        previous: 3490,
        current: 4300,
    },
];

function Sales() {
    return (
        <div className='grid h-full grid-rows-[auto_auto_1fr] gap-4 rounded-lg border bg-white p-4 shadow-lg'>
            <div className='flex items-start justify-between gap-4'>
                <h1 className='text-lg'>Sales</h1>
                <Select>
                    <SelectTrigger className='w-[144px] bg-black/5 text-black/40'>
                        <SelectValue placeholder='Filter' />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value='weekly'>Weekly</SelectItem>
                        <SelectItem value='monthly'>Monthly</SelectItem>
                        <SelectItem value='quarterly'>Quarterly</SelectItem>
                        <SelectItem value='yearly'>Yearly</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div className='flex justify-end gap-4'>
                <div className='flex items-center gap-4'>
                    <div className='h-[15px] w-[15px] rounded-full bg-indigo-300'></div>
                    <p>Previous</p>
                </div>
                <div className='flex items-center gap-4'>
                    <div className='h-[15px] w-[15px] rounded-full bg-green-500'></div>
                    <p>Current</p>
                </div>
            </div>
            <ResponsiveContainer width='100%' height='100%'>
                <BarChart width={500} height={300} data={data}>
                    <CartesianGrid strokeDasharray='3 3' />
                    <XAxis dataKey='name' />
                    <YAxis />
                    <Tooltip />
                    <Bar
                        dataKey='previous'
                        fill='#a5b4fc'
                        activeBar={<Rectangle fill='pink' stroke='blue' />}
                        radius={5}
                    />
                    <Bar
                        dataKey='current'
                        fill='#22c55e'
                        activeBar={<Rectangle fill='gold' stroke='purple' />}
                        radius={5}
                    />
                </BarChart>
            </ResponsiveContainer>
        </div>
    );
}

export default Sales;
