import { z } from 'zod';
import { useState } from 'react';
import {
    DndContext,
    PointerSensor,
    TouchSensor,
    closestCorners,
    useSensor,
    useSensors,
} from '@dnd-kit/core';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Separator } from '@/components/ui/separator';

import { toolboxItems } from './components/toolboxItems';
import Toolbox from './components/Toolbox';
import Dropzone from './components/Dropzone';
import FormBuilderLayout from '@/layouts/FormBuilderLayout';
import OverlayWrapper from './components/OverlayWrapper';
import Properties from './components/Properties';
import PagesSelector from './components/PagesSelector';
import PageEditors from './components/PageEditors';
import useMenuToggle from './hooks/useMenuToggle';
import Sidebar from './components/Sidebar';

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
    const sidebar = useMenuToggle();
    const [active, setActive] = useState('pages');

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
        <DndContext collisionDetection={closestCorners} sensors={sensors}>
            <div className='select-none overflow-hidden bg-[#f4f4f4]'>
                <div className='grid h-full grid-cols-[390px_1fr_auto_300px] gap-x-4 overflow-hidden'>
                    {sidebar.isOpen ? (
                        <Sidebar />
                    ) : (
                        <Tabs
                            defaultValue={active}
                            className='grid grid-rows-[auto_1fr] overflow-auto bg-white p-6'
                            onValueChange={setActive}
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
                            <TabsContent value='components'>
                                <Toolbox items={toolboxItems} />
                            </TabsContent>
                            <TabsContent value='pages'>
                                <PagesSelector />
                            </TabsContent>
                        </Tabs>
                    )}
                    <Dropzone />
                    <Separator
                        orientation='vertical'
                        className='h-full w-[2px]'
                    />

                    {active === 'components' ? <Properties /> : <PageEditors />}
                </div>
                <OverlayWrapper />
            </div>
        </DndContext>
    );
}

FormBuilder.layout = (page: any) => (
    <FormBuilderLayout>{page}</FormBuilderLayout>
);

export default FormBuilder;
