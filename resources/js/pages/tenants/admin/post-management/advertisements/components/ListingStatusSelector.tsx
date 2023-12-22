import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

type ListingStatusSelectorProps = {
    value: string;
    onChange: (value: string) => void;
    color: string;
    data: { label: string; value: string }[];
};

export function ListingStatusSelector({
    value,
    onChange,
    color,
    data,
}: ListingStatusSelectorProps) {
    return (
        <Select value={value} onValueChange={onChange}>
            <SelectTrigger
                style={{ background: color }}
                className='h-6 w-[180px] rounded-xl px-4 ring-0 focus:ring-0'
            >
                <SelectValue placeholder='Select Status' />
            </SelectTrigger>
            <SelectContent>
                <SelectGroup>
                    {data.map((d) => (
                        <SelectItem key={d.value} value={d.value}>
                            {d.label}
                        </SelectItem>
                    ))}
                </SelectGroup>
            </SelectContent>
        </Select>
    );
}
