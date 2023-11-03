import { useState } from 'react';
import { MenuIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';

import useMenuToggle from '@/pages/tenants/admin/listings/form-builder/hooks/useMenuToggle';
import { SavedLogo, SavingLogo } from '@/assets/form-builder';

function FormBuilderHeader() {
    const [saving, setSaving] = useState(false);
    const { isOpen, toggleMenu } = useMenuToggle();

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
            <div className='flex gap-2 p-4'>
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
