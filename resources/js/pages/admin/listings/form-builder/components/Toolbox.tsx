import {
    CalendarIcon,
    CheckSquareIcon,
    ChevronDownSquareIcon,
    CircleIcon,
    FileDigitIcon,
    FileTypeIcon,
    ListChecksIcon,
    LucideIcon,
    TypeIcon,
    UploadIcon,
} from 'lucide-react';

const toolboxItems = [
    { title: 'Text', icon: TypeIcon },
    { title: 'TextArea', icon: FileTypeIcon },
    { title: 'Dropdown', icon: ChevronDownSquareIcon },
    { title: 'Checkbox', icon: CheckSquareIcon },
    { title: 'Checkbox Group', icon: ListChecksIcon },
    { title: 'Radio Group', icon: CircleIcon },
    { title: 'File Upload', icon: UploadIcon },
    { title: 'Number', icon: FileDigitIcon },
    { title: 'Date', icon: CalendarIcon },
];

function Toolbox() {
    return (
        <div className='rounded-lg border p-4 shadow-lg'>
            <h2 className='mb-4 text-center text-[22px]'>Toolbox</h2>
            <div className='space-y-2'>
                {toolboxItems.map((toolboxItem) => (
                    <ToolboxItem {...toolboxItem} />
                ))}
            </div>
        </div>
    );
}

type ToolBoxItemProps = {
    title: string;
    icon: LucideIcon;
};
function ToolboxItem({ title, ...props }: ToolBoxItemProps) {
    return (
        <div
            draggable
            className='flex items-center justify-between gap-2 rounded-lg border-2 border-dashed bg-blue-50 px-4 py-3'
        >
            {title}
            <props.icon />
        </div>
    );
}

export default Toolbox;
