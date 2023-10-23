import { z } from 'zod';
import {
    DndContext,
    PointerSensor,
    TouchSensor,
    useSensor,
    useSensors,
} from '@dnd-kit/core';
import { LucideIcon } from 'lucide-react';

import Toolbox from './components/Toolbox';
import Dropzone from './components/Dropzone';
import ToolboxItem from './components/Toolbox/ToolBoxItem';
import FormBuilderLayout from '@/layouts/FormBuilderLayout';
import { toolboxItems } from './components/toolboxItems';
import OverlayWrapper from './components/OverlayWrapper';

const formSchema = z.object({
    custom_fields: z.array(
        z.object({
            allow_multiple_option_answer: z.boolean(),
            type: z.string(),
            label: z.string(),
            is_required: z.boolean(),
            options: z.array(z.object({ value: z.string() })),
        }),
    ),
});

export type FormbuilderForm = z.infer<typeof formSchema>;
export type FormFields = FormbuilderForm['custom_fields'][0] & { id: string };
type ToolboxItem = { title: string; icon: LucideIcon; id: string };

const defaultValues: FormbuilderForm = {
    custom_fields: [],
};

type ActiveToolbox = {
    id: string;
    toolBoxItem: ToolboxItem;
};

function FormBuilder() {
    const touchSensor = useSensor(TouchSensor, {
        activationConstraint: {
            delay: 300,
            tolerance: 5,
        },
    });

    const mouseSensor = useSensor(PointerSensor, {
        activationConstraint: {
            distance: 10,
        },
    });

    const sensors = useSensors(mouseSensor, touchSensor);

    return (
        <div className='overflow-hidden'>
            <DndContext sensors={sensors}>
                <div className='grid h-full grid-cols-[390px_1fr_300px] overflow-hidden'>
                    <Toolbox items={toolboxItems} />
                    <Dropzone />
                </div>
                <OverlayWrapper />
            </DndContext>
        </div>
    );
}

FormBuilder.layout = (page: any) => (
    <FormBuilderLayout>{page}</FormBuilderLayout>
);

export default FormBuilder;
