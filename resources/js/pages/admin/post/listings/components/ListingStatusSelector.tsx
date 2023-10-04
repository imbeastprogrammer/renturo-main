import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { cn } from "@/lib/utils";

type Status = "posted" | "to review" | "declined";

type ListingStatusSelectorProps = {
    value: Status;
    onChange: (value: Status) => void;
};

const statusColor: Record<Status, string> = {
    posted: "#B1EEB7",
    "to review": "#FBDF88",
    declined: "#FFA1A1",
};

export function ListingStatusSelector({
    value,
    onChange,
}: ListingStatusSelectorProps) {
    return (
        <Select value={value} onValueChange={onChange}>
            <SelectTrigger
                style={{ background: statusColor[value] }}
                className="w-[180px] px-4 h-6 ring-0 rounded-xl focus:ring-0"
            >
                <SelectValue placeholder="Select Status" />
            </SelectTrigger>
            <SelectContent>
                <SelectGroup>
                    <SelectItem value="posted">Posted</SelectItem>
                    <SelectItem value="to review">To Review</SelectItem>
                    <SelectItem value="declined">Declined</SelectItem>
                </SelectGroup>
            </SelectContent>
        </Select>
    );
}
