import React from 'react';

type PropertyEditorHandleProps = {
    type: string;
    icon: React.FC;
};
function PropertyEditorHandle({ icon: Icon, type }: PropertyEditorHandleProps) {
    return (
        <div className='flex items-center gap-4 text-[12px]'>
            <div className='grid h-[35px] w-[35px] place-items-center rounded-lg bg-metalic-blue/10 text-metalic-blue'>
                {Icon && <Icon />}
            </div>
            {type}
        </div>
    );
}

export default PropertyEditorHandle;
