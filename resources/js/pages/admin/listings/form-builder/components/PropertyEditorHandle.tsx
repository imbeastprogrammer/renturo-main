import { LucideIcon } from 'lucide-react';

type PropertyEditorHandleProps = {
    type: string;
    icon: LucideIcon;
};
function PropertyEditorHandle({ icon: Icon, type }: PropertyEditorHandleProps) {
    return (
        <div className='flex items-center gap-4 text-[12px]'>
            <div className='grid h-[30px] w-[30px] place-items-center rounded-lg bg-metalic-blue/10 text-metalic-blue'>
                {Icon && <Icon className='h-[19px] w-[19px]' />}
            </div>
            {type}
        </div>
    );
}

export default PropertyEditorHandle;
