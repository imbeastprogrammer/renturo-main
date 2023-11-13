import { cn } from "@/lib/utils";

type ListingFilterProps = {
    value: string;
    data: { label: string; value: string }[];
    onChange: (value: string) => void;
};

function ListingFilter({ data, value, onChange }: ListingFilterProps) {
    return (
        <ul className="flex gap-4">
            {data.map((d) => (
                <li key={d.value}>
                    <button
                        onClick={() => onChange(d.value)}
                        className={cn(
                            "text-headline-4 p-2 transition border-b border-transparent text-heavy-carbon",
                            {
                                "text-metalic-blue border-metalic-blue":
                                    value === d.value,
                            }
                        )}
                    >
                        {d.label}
                    </button>
                </li>
            ))}
        </ul>
    );
}

export default ListingFilter;
