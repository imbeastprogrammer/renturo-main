import { LucideIcon } from 'lucide-react';

type PropertyEditorHandleProps = {
    type: string;
    icon: LucideIcon;
};
function PropertyEditorHandle({ icon: Icon, type }: PropertyEditorHandleProps) {
    return (
        <div className='flex items-center gap-4'>
            <div className='grid h-10 w-10 place-items-center rounded-lg bg-metalic-blue/10 text-metalic-blue'>
                {Icon && <Icon />}
            </div>
            {type}
        </div>
    );
}

export default PropertyEditorHandle;
