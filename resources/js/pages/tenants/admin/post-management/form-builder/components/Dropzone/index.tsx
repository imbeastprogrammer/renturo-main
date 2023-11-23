import { v4 as uuidv4 } from 'uuid';
import { TiPlus } from 'react-icons/ti';
import { Separator } from '@/components/ui/separator';
import {
    DragEndEvent,
    useDndMonitor,
    useDraggable,
    useDroppable,
} from '@dnd-kit/core';
import {
    ElementsType,
    FormElementInstance,
    FormElements,
} from '../FormElement';
import { cn } from '@/lib/utils';

import { DesignerGripIcon } from '@/assets/form-builder';
import useFormBuilder from '@/hooks/useFormBuilder';
import EmptyDropzone from './EmptyDropzone';

function Dropzone() {
    const { pages, addField, removeField, current_page_id } = useFormBuilder();
    const currentPage = pages.find((page) => page.page_id === current_page_id);

    const { setNodeRef, isOver } = useDroppable({
        id: 'designer-drop-area',
        data: {
            isDesignerDropArea: true,
        },
    });

    useDndMonitor({
        onDragEnd: (event: DragEndEvent) => {
            const { active, over } = event;
            if (!active || !over || !currentPage) return;

            const isToolboxItem = active?.data.current?.isToolboxItem;

            const isDesignerDroppingArea =
                over?.data.current?.isDesignerDropArea;

            if (isToolboxItem && isDesignerDroppingArea) {
                const type = active.data?.current?.type;
                const newField = FormElements[type as ElementsType].construct(
                    uuidv4(),
                );
                return addField(
                    current_page_id,
                    (currentPage?.fields || []).length,
                    newField,
                );
            }

            const isDroppablePlaceholder =
                over.data.current?.isDroppablePlaceholder;

            if (isToolboxItem && isDroppablePlaceholder) {
                const type = active.data?.current?.type;
                const newElement = FormElements[type as ElementsType].construct(
                    uuidv4(),
                );

                const overElementIndex = over.data?.current?.index + 1;

                if (overElementIndex === -1) {
                    throw new Error('element not found');
                }

                return addField(current_page_id, overElementIndex, newElement);
            }

            const isDraggingDesignerElement =
                active.data?.current?.isDesignerElement;

            if (isDraggingDesignerElement && isDroppablePlaceholder) {
                const activeId = active.data?.current?.elementId;

                const activeElementIndex = currentPage.fields.findIndex(
                    (el) => el.id === activeId,
                );

                let overElementIndex = over.data?.current?.index;

                if (activeElementIndex === -1 || overElementIndex === -1) {
                    throw new Error('element not found');
                }

                const activeElement = {
                    ...currentPage.fields[activeElementIndex],
                };

                removeField(current_page_id, activeId);

                return addField(
                    current_page_id,
                    overElementIndex,
                    activeElement,
                );
            }
        },
    });

    return (
        <div ref={setNodeRef} className='relative overflow-hidden'>
            <div className='relative h-full overflow-x-hidden overflow-y-scroll bg-[#f4f4f4] px-4 py-8 shadow-lg'>
                {currentPage?.fields.map((field, idx) => (
                    <DesignerElementWrapper
                        key={field.id}
                        index={idx}
                        element={field}
                    />
                ))}
                {!currentPage?.fields.length && !isOver && <EmptyDropzone />}
                {isOver && (
                    <div className='grid h-[143px] place-items-center rounded-lg border-2 border-dashed border-metalic-blue bg-metalic-blue/5 text-[15px] font-medium text-metalic-blue'>
                        Drag and drop elements from the left to add a new
                        component
                    </div>
                )}
            </div>
        </div>
    );
}

type DropppablePlaceholderProps = {
    index: number;
};
function DroppablePlaceholder({ index }: DropppablePlaceholderProps) {
    const { setNodeRef, isOver } = useDroppable({
        id: 'droppable-placeholder' + index,
        data: { index, isDroppablePlaceholder: true },
    });

    return (
        <div className='relative grid gap-2 py-2' ref={setNodeRef}>
            <div className='flex items-center'>
                <Separator className='h-[2px] flex-1' />
                <div className='grid h-[20px] w-[20px] place-items-center rounded-full bg-[#E1E1E1] text-[#2E3436]'>
                    <TiPlus />
                </div>
                <Separator className='h-[2px] flex-1' />
            </div>
            {isOver && (
                <div className='grid h-[143px] place-items-center rounded-lg border-2 border-dashed border-metalic-blue bg-metalic-blue/5 text-[15px] font-medium text-metalic-blue'>
                    Drag and drop elements from the left to add a new component
                </div>
            )}
        </div>
    );
}

type DesignerElementWrapperProps = {
    element: FormElementInstance;
    index: number;
};

function DesignerElementWrapper({
    element,
    index,
}: DesignerElementWrapperProps) {
    const { selectedField } = useFormBuilder();

    const draggable = useDraggable({
        id: element.id + '-draggable-handler',
        data: {
            type: element.type,
            elementId: element.id,
            isDesignerElement: true,
        },
    });

    const isActive = selectedField?.id === element.id;

    if (draggable.isDragging) return null;

    const DesignerElement = FormElements[element.type].designerComponent;

    return (
        <>
            <div
                ref={draggable.setNodeRef}
                {...draggable.attributes}
                className={cn(
                    'relative flex items-center gap-8 rounded-lg border bg-white p-4',
                    isActive && 'ring-2',
                )}
            >
                <div {...draggable.listeners} {...draggable.attributes}>
                    <DesignerGripIcon />
                </div>
                <DesignerElement element={element} />
            </div>
            <DroppablePlaceholder index={index} />
        </>
    );
}

export default Dropzone;
