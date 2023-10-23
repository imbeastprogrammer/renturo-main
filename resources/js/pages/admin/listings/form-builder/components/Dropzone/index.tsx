import {
    DragOverEvent,
    useDndMonitor,
    useDraggable,
    useDroppable,
} from '@dnd-kit/core';
import {
    ElementsType,
    FormElementInstance,
    FormElements,
} from '../FormElement';
import useFormBuilder from '@/hooks/useFormBuilder';

function Dropzone() {
    const { fields, addField } = useFormBuilder();

    const { setNodeRef } = useDroppable({
        id: 'designer-drop-area',
        data: {
            isDesignerDropArea: true,
        },
    });

    useDndMonitor({
        onDragOver: (event: DragOverEvent) => {
            const { active, over } = event;
            const isDesignerDroppingArea =
                over?.data.current?.isDesignerDropArea;

            if (isDesignerDroppingArea) {
                const type = active.data?.current?.type;
                const newField = FormElements[type as ElementsType].construct(
                    Date.now().toString(),
                );
                addField(fields.length, newField);
            }
        },
    });

    return (
        <div ref={setNodeRef} className='relative overflow-hidden'>
            <div className='hide-scrollbar relative h-full space-y-2 overflow-y-auto overflow-x-hidden bg-[#f4f4f4] p-8 shadow-lg'>
                {fields.map((field) => (
                    <DesignerElementWrapper key={field.id} element={field} />
                ))}
            </div>
        </div>
    );
}

function DesignerElementWrapper({ element }: { element: FormElementInstance }) {
    const topHalf = useDroppable({
        id: element.id + '-top',
        data: {
            type: element.type,
            elementId: element.id,
            isTopHalfDesignerElement: true,
        },
    });

    const bottomHalf = useDroppable({
        id: element.id + '-bottom',
        data: {
            type: element.type,
            elementId: element.id,
            isBottomHalfDesignerElement: true,
        },
    });

    const draggable = useDraggable({
        id: element.id + '-drag-handler',
        data: {
            type: element.type,
            elementId: element.id,
            isDesignerElement: true,
        },
    });

    if (draggable.isDragging) return null;

    const DesignerElement = FormElements[element.type].designerComponent;

    return (
        <div
            ref={draggable.setNodeRef}
            {...draggable.listeners}
            {...draggable.attributes}
            className='relative'
        >
            <div
                ref={topHalf.setNodeRef}
                className='absolute -top-full h-1/2 w-full rounded-t-md'
            />
            <div
                ref={bottomHalf.setNodeRef}
                className='absolute -top-full bottom-0 h-1/2 w-full rounded-b-md'
            />

            {topHalf.isOver && (
                <div className='bg-primary absolute top-0 h-[7px] w-full rounded-md rounded-b-none' />
            )}
            <DesignerElement element={element} />
            {bottomHalf.isOver && (
                <div className='bg-primary absolute bottom-0 h-[7px] w-full rounded-md rounded-t-none' />
            )}
        </div>
    );
}

export default Dropzone;
