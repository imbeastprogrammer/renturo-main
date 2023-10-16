import { z } from 'zod';
import { useState } from 'react';
import { useFieldArray, useForm } from 'react-hook-form';
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
import DropzoneFieldArray from './components/DropzoneFieldArray';
import ToolboxItem from './components/ToolBoxItem';
import { zodResolver } from '@hookform/resolvers/zod';

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

const formSchema = z.object({
    custom_fields: z.array(
        z.object({
            type: z.string(),
            label: z.string(),
            name: z.string(),
            placeholder: z.string(),
            min: z.string(),
            max: z.string(),
            is_required: z.boolean(),
        }),
    ),
});

export type FormbuilderForm = z.infer<typeof formSchema>;
export type FormFields = FormbuilderForm['custom_fields'];

const defaultValues: FormbuilderForm = {
    custom_fields: [],
};

function FormBuilder() {
    const [dragging, setDragging] = useState(false);
    const [active, setActive] = useState('');

    const activeDraggingItem = toolboxItems.find((item) => item.id === active);

    const form = useForm<FormbuilderForm>({
        defaultValues,
        resolver: zodResolver(formSchema),
    });

    const fieldArray = useFieldArray({
        name: 'custom_fields',
        control: form.control,
    });

    console.log(fieldArray.fields);

    const handleDragStart = (event: DragStartEvent) => {
        const { active } = event;
        setActive(active.id.toString());
        setDragging(true);
    };
    const handleDragEnd = (event: DragOverEvent) => {
        const { active, over } = event;
        setDragging(false);

        if (over?.id === 'droppable') {
            fieldArray.append({
                type: active.id.toString(),
                label: '',
                name: '',
                placeholder: '',
                min: '',
                max: '',
                is_required: false,
            });
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
                            <DropzoneFieldArray
                                items={fieldArray.fields}
                                isDragging={dragging}
                                onRemove={(idx) => fieldArray.remove(idx)}
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
