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

import { Form } from '@/components/ui/form';
import Toolbox from './components/Toolbox';
import DropzoneFieldArray from './components/DropzoneFieldArray';
import ToolboxItem from './components/ToolBoxItem';
import FormBuilderLayout from '@/layouts/FormBuilderLayout';
import EmptyDropzone from './components/EmptyDropzone';
import { toolboxItems } from './components/toolboxItems';

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

type ActiveToolbox = {
    id: string;
    container: string;
};

function FormBuilder() {
    const [dragging, setDragging] = useState(false);
    const [active, setActive] = useState<ActiveToolbox | null>(null);

    const activeContainer = toolboxItems.find(
        ({ label }) => label === active?.container,
    );
    const activeToolboxItem = activeContainer?.items.find(
        (item) => item.id === active?.id,
    );

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
        setActive(active.data.current as ActiveToolbox);
        setDragging(true);
    };

    const handleDragEnd = (event: DragEndEvent) => {
        const { active, over } = event;
        setDragging(false);
        setActive(null);

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
                        <div className='grid h-full grid-cols-[390px_1fr] overflow-hidden'>
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
                        {activeContainer && activeToolboxItem && (
                            <DragOverlay>
                                <ToolboxItem
                                    {...activeToolboxItem}
                                    container={activeContainer?.label}
                                />
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
