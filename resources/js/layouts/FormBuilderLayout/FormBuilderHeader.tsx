import _ from 'lodash';
import { useState } from 'react';
import { MenuIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';

import useMenuToggle from '@/pages/tenants/admin/listings/form-builder/hooks/useMenuToggle';
import {
    RedoIcon,
    SavedLogo,
    SavingLogo,
    UndoIcon,
} from '@/assets/form-builder';
import { useFormbuilderWithUndoRedo } from '@/hooks/useFormBuilder';

function FormBuilderHeader() {
    const [saving, setSaving] = useState(false);
    const { isOpen, toggleMenu } = useMenuToggle();
    const { undo, redo, pastStates, futureStates } = useFormbuilderWithUndoRedo(
        (state) => state,
    );

    const handleMenuToggle = () => toggleMenu(isOpen);
    const handleSave = () => {
        setSaving(true);

        setTimeout(() => {
            setSaving(false);
        }, 3000);
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
                {saving ? <SavingLogo /> : <SavedLogo />}
            </div>
            <div className='flex items-center gap-4 p-4'>
                <button
                    disabled={!pastStates.length}
                    className='grid h-[46px] w-[46px] place-items-center rounded-full transition hover:bg-gray-100 disabled:pointer-events-none disabled:opacity-50'
                    onClick={() => undo()}
                >
                    <UndoIcon />
                </button>
                <button
                    disabled={!futureStates.length}
                    className='grid h-[46px] w-[46px] place-items-center rounded-full transition hover:bg-gray-100 disabled:pointer-events-none disabled:opacity-50'
                    onClick={() => redo()}
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
