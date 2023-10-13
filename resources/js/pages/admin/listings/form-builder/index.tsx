import { useState } from 'react';
import {
    DndContext,
    DragOverEvent,
    DragOverlay,
    DragStartEvent,
} from '@dnd-kit/core';
import {
    CalendarIcon,
    CheckSquareIcon,
    ChevronDownSquareIcon,
    CircleIcon,
    FileDigitIcon,
    FileTypeIcon,
    ListChecksIcon,
    TypeIcon,
    UploadIcon,
} from 'lucide-react';

import { Form } from '@/components/ui/form';
import AdminLayout from '@/layouts/AdminLayout';
import Toolbox from './components/Toolbox';
import Dropzone from './components/Dropzone';
import ToolboxItem from './components/ToolBoxItem';
import { useForm } from 'react-hook-form';

const toolboxItems = [
    { title: 'Text', icon: TypeIcon, id: 'text' },
    { title: 'TextArea', icon: FileTypeIcon, id: 'textarea' },
    { title: 'Dropdown', icon: ChevronDownSquareIcon, id: 'dropdown' },
    { title: 'Checkbox', icon: CheckSquareIcon, id: 'checkbox' },
    { title: 'Checkbox Group', icon: ListChecksIcon, id: 'checkbox-group' },
    { title: 'Radio Group', icon: CircleIcon, id: 'radio-group' },
    { title: 'File Upload', icon: UploadIcon, id: 'file-upload' },
    { title: 'Number', icon: FileDigitIcon, id: 'number' },
    { title: 'Date', icon: CalendarIcon, id: 'date' },
];

export type DropzoneField = {
    id: string;
    type: string;
    label: string;
    name: string;
    placeholder: string;
    min: string;
    max: string;
    is_required: boolean;
};

function FormBuilder() {
    const [dragging, setDragging] = useState(false);
    const [active, setActive] = useState('');
    const [dropzoneItems, setDropzoneItems] = useState<DropzoneField[]>([]);

    const activeDraggingItem = toolboxItems.find((item) => item.id === active);

    const form = useForm();

    const handleDragStart = (event: DragStartEvent) => {
        const { active } = event;
        setActive(active.id.toString());
        setDragging(true);
    };
    const handleDragEnd = (event: DragOverEvent) => {
        const { active, over } = event;
        setDragging(false);

        if (over?.id === 'droppable') {
            setDropzoneItems((prev) => [
                ...prev,
                {
                    id: Date.now().toString(),
                    type: active.id.toString(),
                    label: '',
                    name: '',
                    placeholder: '',
                    min: '',
                    max: '',
                    is_required: false,
                },
            ]);
            setActive('');
        }
    };

    const handleSubmit = form.handleSubmit((data) => {});

    return (
        <div className='overflow-hidden'>
            <Form {...form}>
                <form
                    onSubmit={handleSubmit}
                    className='grid h-full grid-rows-[auto_1fr] gap-4 overflow-hidden'
                >
                    <h1 className='text-[30px] font-semibold'>Form Builders</h1>
                    <div className='grid h-full grid-cols-[300px_1fr] gap-x-4 overflow-hidden'>
                        <DndContext
                            onDragStart={handleDragStart}
                            onDragEnd={handleDragEnd}
                        >
                            <Toolbox items={toolboxItems} />
                            <Dropzone
                                items={dropzoneItems}
                                isDragging={dragging}
                            />
                            <DragOverlay>
                                {activeDraggingItem && (
                                    <ToolboxItem {...activeDraggingItem} />
                                )}
                            </DragOverlay>
                        </DndContext>
                    </div>
                </form>
            </Form>
        </div>
    );
}

FormBuilder.layout = (page: any) => <AdminLayout>{page}</AdminLayout>;

export default FormBuilder;
