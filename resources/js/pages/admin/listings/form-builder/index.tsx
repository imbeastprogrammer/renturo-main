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
import { LucideIcon } from 'lucide-react';

import { Form } from '@/components/ui/form';
import Toolbox from './components/Toolbox';
import DropzoneFieldArray from './components/DropzoneFieldArray';
import ToolboxItem from './components/Toolbox/ToolBoxItem';
import FormBuilderLayout from '@/layouts/FormBuilderLayout';
import { toolboxItems } from './components/toolboxItems';

const formSchema = z.object({
    custom_fields: z.array(
        z.object({
            type: z.string(),
            label: z.string(),
            options: z.array(z.string()),
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
    toolBoxItem: { title: string; icon: LucideIcon; id: string };
};

function FormBuilder() {
    const [dragging, setDragging] = useState(false);
    const [active, setActive] = useState<ActiveToolbox | null>(null);

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
        setDragging(true);
        setActive({
            id: active.data.current?.id,
            toolBoxItem: active.data.current?.toolboxItem,
        });
    };

    const handleDragEnd = (event: DragEndEvent) => {
        const { active, over } = event;
        setDragging(false);
        setActive(null);

        if (over?.id === 'droppable')
            fieldArray.append({
                type: active.id.toString(),
                label: 'This label is editable',
                options: [],
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
                        <div className='grid h-full grid-cols-[390px_1fr_300px] overflow-hidden'>
                            <Toolbox items={toolboxItems} />
                            <DropzoneFieldArray
                                items={fieldArray.fields}
                                isDragging={dragging}
                                onRemove={(idx) => fieldArray.remove(idx)}
                                onSort={(active, over) =>
                                    fieldArray.swap(active, over)
                                }
                            />
                        </div>
                        {active?.toolBoxItem && (
                            <DragOverlay>
                                <ToolboxItem {...active.toolBoxItem} />
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
