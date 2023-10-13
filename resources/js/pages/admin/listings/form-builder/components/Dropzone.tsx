import { useDroppable } from '@dnd-kit/core';

type DropzoneProps = { items: string[] };
function Dropzone({ items }: DropzoneProps) {
    const { setNodeRef } = useDroppable({
        id: 'droppable',
    });

    return (
        <div
            ref={setNodeRef}
            className='rounded-lg border-2 border-dashed bg-blue-50 p-4 shadow-lg'
        >
            <h1 className='text-center text-[20px] text-gray-500'>
                Select / Drop item from toolbox
            </h1>
            <div>
                {items.map((item, i) => (
                    <p key={item + i}>{item}</p>
                ))}
            </div>
        </div>
    );
}

export default Dropzone;
