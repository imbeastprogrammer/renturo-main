import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";

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
                className="w-[180px] px-4 h-6 ring-0 rounded-xl focus:ring-0"
            >
                <SelectValue placeholder="Select Status" />
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
