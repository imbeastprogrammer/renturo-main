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

import AdminLayout from '@/layouts/AdminLayout';
import Toolbox from './components/Toolbox';
import Dropzone from './components/Dropzone';
import ToolboxItem from './components/ToolBoxItem';

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

function FormBuilder() {
    const [active, setActive] = useState('');
    const [items, setItems] = useState<string[]>([]);

    const activeDraggingItem = toolboxItems.find((item) => item.id === active);

    const handleDragStart = (event: DragStartEvent) => {
        const { active } = event;
        setActive(active.id.toString());
    };
    const handleDragEnd = (event: DragOverEvent) => {
        const { active, over } = event;

        if (over?.id === 'droppable') {
            setItems((prev) => [...prev, active.id.toString()]);
            setActive('');
        }
    };

    return (
        <div>
            <h1 className='mb-4 text-[30px] font-semibold'>Form Builders</h1>
            <DndContext onDragStart={handleDragStart} onDragEnd={handleDragEnd}>
                <div className='grid grid-cols-[300px_1fr] gap-x-4'>
                    <Toolbox items={toolboxItems} />
                    <Dropzone items={items} />
                </div>
                <DragOverlay>
                    {activeDraggingItem && (
                        <ToolboxItem {...activeDraggingItem} />
                    )}
                </DragOverlay>
            </DndContext>
        </div>
    );
}

FormBuilder.layout = (page: any) => <AdminLayout>{page}</AdminLayout>;

export default FormBuilder;
