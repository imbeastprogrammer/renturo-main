import { z } from 'zod';
import { useState } from 'react';
import { zodResolver } from '@hookform/resolvers/zod';
import { useFieldArray, useForm } from 'react-hook-form';
import {
    DndContext,
    DragEndEvent,
    DragOverlay,
    DragStartEvent,
    PointerSensor,
    useSensor,
    useSensors,
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
import Toolbox from './components/Toolbox';
import DropzoneFieldArray from './components/DropzoneFieldArray';
import ToolboxItem from './components/ToolBoxItem';
import FormBuilderLayout from '@/layouts/FormBuilderLayout';
import EmptyDropzone from './components/EmptyDropzone';

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
export type FormFields = FormbuilderForm['custom_fields'][0] & { id: string };

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

    const sensors = useSensors(
        useSensor(PointerSensor, {
            activationConstraint: {
                distance: 10,
            },
        }),
    );

    const handleDragStart = (event: DragStartEvent) => {
        const { active } = event;
        setActive(active.id.toString());
        setDragging(true);
    };

    const handleDragEnd = (event: DragEndEvent) => {
        const { active, over } = event;
        setDragging(false);
        setActive('');

        if (over?.id === 'droppable')
            fieldArray.append({
                type: active.id.toString(),
                label: '',
                name: '',
                placeholder: '',
                min: '',
                max: '',
                is_required: false,
            });
    };

    const handleSubmit = form.handleSubmit((data) => {});

    return (
        <div className='overflow-hidden'>
            <Form {...form}>
                <form
                    onSubmit={handleSubmit}
                    className='h-full overflow-hidden'
                >
                    <DndContext
                        sensors={sensors}
                        onDragStart={handleDragStart}
                        onDragEnd={handleDragEnd}
                    >
                        <div className='grid h-full grid-cols-[350px_1fr] overflow-hidden'>
                            <Toolbox items={toolboxItems} />
                            <EmptyDropzone />
                            {/* <DropzoneFieldArray
                                items={fieldArray.fields}
                                isDragging={dragging}
                                onRemove={(idx) => fieldArray.remove(idx)}
                                onSort={(active, over) =>
                                    fieldArray.swap(active, over)
                                }
                            /> */}
                        </div>
                        {activeDraggingItem && (
                            <DragOverlay>
                                <ToolboxItem {...activeDraggingItem} />
                            </DragOverlay>
                        )}
                    </DndContext>
                </form>
            </Form>
        </div>
    );
}

FormBuilder.layout = (page: any) => (
    <FormBuilderLayout>{page}</FormBuilderLayout>
);

export default FormBuilder;
