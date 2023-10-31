import { z } from 'zod';
import {
    DndContext,
    PointerSensor,
    TouchSensor,
    closestCenter,
    useSensor,
    useSensors,
} from '@dnd-kit/core';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

import { toolboxItems } from './components/toolboxItems';
import Toolbox from './components/Toolbox';
import Dropzone from './components/Dropzone';
import FormBuilderLayout from '@/layouts/FormBuilderLayout';
import OverlayWrapper from './components/OverlayWrapper';
import Properties from './components/Properties';
import PagesSelector from './components/PagesSelector';

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
            <DndContext collisionDetection={closestCenter} sensors={sensors}>
                <div className='grid h-full grid-cols-[390px_1fr_300px] overflow-hidden'>
                    <Tabs
                        defaultValue='components'
                        className='grid grid-rows-[auto_1fr] gap-y-4 overflow-hidden p-6'
                    >
                        <TabsList className='h-max w-full rounded-full'>
                            <TabsTrigger
                                value='components'
                                className='w-full rounded-full text-[20px]'
                            >
                                Components
                            </TabsTrigger>
                            <TabsTrigger
                                value='pages'
                                className='w-full rounded-full text-[20px]'
                            >
                                Pages
                            </TabsTrigger>
                        </TabsList>
                        <TabsContent
                            value='components'
                            className='hide-scrollbar overflow-auto'
                        >
                            <Toolbox items={toolboxItems} />
                        </TabsContent>
                        <TabsContent value='pages'>
                            <PagesSelector />
                        </TabsContent>
                    </Tabs>
                    <Dropzone />
                    <Properties />
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
