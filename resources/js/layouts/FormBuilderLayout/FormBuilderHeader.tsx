import _ from 'lodash';
import { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { MenuIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';

import {
    RedoIcon,
    SavedIcon,
    SavingIcon,
    UndoIcon,
} from '@/assets/form-builder';
import useFormBuilder from '@/hooks/useFormBuilder';
import useMenuToggle from '@/pages/tenants/admin/post-management/dynamic-forms/form-builder/hooks/useMenuToggle';
import useUndoAndRedoFormbuilderByKeyPress from '@/hooks/useUndoAndRedoFormbuilderByKeyPress';

interface DynamicForm {
    id: number;
    name: string;
    subcategory_id: number;
}

function FormBuilderHeader() {
    const props = usePage().props;
    const dynamicForm = props.dynamicForm as DynamicForm;
    const [saving, setSaving] = useState(false);
    const { isOpen, toggleMenu } = useMenuToggle();
    const { pages, history, future, undo, redo } = useFormBuilder();
    useUndoAndRedoFormbuilderByKeyPress({ undo, redo });

    console.log(dynamicForm);

    const handleMenuToggle = () => toggleMenu(isOpen);
    const handleSave = () => {
        router.put(
            `/admin/form/all/${dynamicForm.id}`,
            {
                id: dynamicForm.id,
                name: dynamicForm.name,
                subcategory_id: dynamicForm.subcategory_id,
                dynamic_form_pages: pages.map((page) => ({
                    title: page.page_title,
                    dynamic_form_fields: page.fields.map((field) => ({
                        id: typeof field.id === 'string' ? 0 : field.id,
                        input_field_label: field.label,
                        input_field_type: field.type,
                        is_required: field.is_required,
                        data: field.data,
                    })),
                })),
            },
            {
                onBefore: () => setSaving(true),
                onFinish: () => setSaving(false),
            },
        );
    };

    return (
        <header className='grid grid-cols-[390px_1fr_auto] items-center shadow-lg'>
            <div className='flex h-full items-center gap-6 p-4'>
                <div>
                    <button onClick={handleMenuToggle}>
                        <MenuIcon className='h-[40px] w-[40px] text-metalic-blue hover:text-metalic-blue/80' />
                    </button>
                </div>
                <div>
                    <h1 className='text-[30px] font-semibold leading-none'>
                        Form Builder
                    </h1>
                    <p className='text-[20px] text-gray-500'>
                        Add and customize forms
                    </p>
                </div>
            </div>
            <div className='flex h-full items-center gap-8 py-4'>
                <Separator
                    orientation='vertical'
                    className='h-[68px] w-[4px] rounded-lg'
                />
                {saving ? <SavingIcon /> : <SavedIcon />}
            </div>
            <div className='flex items-center gap-4 p-4'>
                <button
                    onClick={undo}
                    disabled={!history.length}
                    className='grid h-[46px] w-[46px] place-items-center rounded-full transition hover:bg-gray-100 disabled:pointer-events-none disabled:opacity-50'
                >
                    <UndoIcon />
                </button>
                <button
                    onClick={redo}
                    disabled={!future.length}
                    className='grid h-[46px] w-[46px] place-items-center rounded-full transition hover:bg-gray-100 disabled:pointer-events-none disabled:opacity-50'
                >
                    <RedoIcon />
                </button>
                <Button
                    onClick={handleSave}
                    variant='outline'
                    className='w-36 text-metalic-blue'
                >
                    Save
                </Button>
                <Button className='w-36 bg-metalic-blue hover:bg-metalic-blue/90'>
                    Publish
                </Button>
            </div>
        </header>
    );
}

export default FormBuilderHeader;
