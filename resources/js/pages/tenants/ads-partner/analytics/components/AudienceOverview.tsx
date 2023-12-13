import {
    AreaChart,
    Area,
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
        name: 'Oct. 1',
        sales: 4000,
        visitor: 2400,
    },
    {
        name: 'Oct. 2',
        sales: 3000,
        visitor: 1398,
    },
    {
        name: 'Oct. 3',
        sales: 2000,
        visitor: 2290,
    },
    {
        name: 'Oct. 4',
        sales: 2780,
        visitor: 3908,
    },
    {
        name: 'Oct. 5',
        sales: 1890,
        visitor: 2181,
    },
    {
        name: 'Oct. 6',
        sales: 8500,
        visitor: 3800,
    },
    {
        name: 'Oct. 7',
        sales: 5100,
        visitor: 4300,
    },
    {
        name: 'Oct. 8',
        sales: 4000,
        visitor: 100,
    },
];
function AudienceOverview() {
    return (
        <div className='grid h-full grid-rows-[auto_auto_1fr] gap-4 rounded-lg border bg-white p-4 shadow-lg'>
            <div className='flex items-start justify-between gap-4'>
                <h1 className='text-lg'>Audience Overview</h1>
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
                    <div className='h-[15px] w-[15px] rounded-full border-2 border-arylide-yellow'></div>
                    <p>Sales</p>
                </div>
                <div className='flex items-center gap-4'>
                    <div className='h-[15px] w-[15px] rounded-full border-2 border-jasper-orange'></div>
                    <p>Visitor</p>
                </div>
            </div>
            <div>
                <ResponsiveContainer width='100%' height='100%'>
                    <AreaChart data={data}>
                        <CartesianGrid strokeDasharray='3 3' />
                        <XAxis dataKey='name' className='text-sm' />
                        <YAxis className='text-sm' />
                        <Tooltip />
                        <Area
                            type='linear'
                            dataKey='sales'
                            stackId='1'
                            stroke='#EDCA5E'
                            fill='#f8cb70'
                            dot
                        />
                        <Area
                            type='linear'
                            dataKey='visitor'
                            stackId='1'
                            stroke='#DC8A4A'
                            fill='#db9d6e'
                            dot
                        />
                    </AreaChart>
                </ResponsiveContainer>
            </div>
        </div>
    );
}

export default AudienceOverview;
