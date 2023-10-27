import { v4 as uuidv4 } from 'uuid';
import { DragEndEvent, useDndMonitor, useDroppable } from '@dnd-kit/core';
import {
    ElementsType,
    FormElementInstance,
    FormElements,
} from '../FormElement';
import useFormBuilder from '@/hooks/useFormBuilder';
import { SortableContext, arrayMove, useSortable } from '@dnd-kit/sortable';

function Dropzone() {
    const { fields, addField, setFields, current_page } = useFormBuilder();

    const { setNodeRef, isOver } = useDroppable({
        id: 'designer-drop-area',
        data: {
            isDesignerDropArea: true,
        },
    });

    useDndMonitor({
        onDragEnd: (event: DragEndEvent) => {
            const { active, over } = event;
            if (!active || !over) return;

            const isToolboxItem = active?.data.current?.isToolboxItem;

            const isDesignerDroppingArea =
                over?.data.current?.isDesignerDropArea;

            if (isToolboxItem && isDesignerDroppingArea) {
                const type = active.data?.current?.type;
                const newField = FormElements[type as ElementsType].construct(
                    uuidv4(),
                    current_page,
                );
                return addField(fields.length, newField);
            }

            const isDroppingOverDesignerElementBottomHalf =
                over.data?.current?.isBottomHalfDesignerElement;
            const isDroppingOverDesignerElementTopHalf =
                over.data?.current?.isTopHalfDesignerElement;

            const isDroppingOverDesignerElement =
                isDroppingOverDesignerElementBottomHalf ||
                isDroppingOverDesignerElementTopHalf;

            if (isToolboxItem && isDroppingOverDesignerElement) {
                const type = active.data?.current?.type;
                const newElement = FormElements[type as ElementsType].construct(
                    uuidv4(),
                    current_page,
                );

                const overId = over.data?.current?.elementId;

                const overElementIndex = fields.findIndex(
                    (el) => el.id === overId,
                );
                if (overElementIndex === -1) {
                    throw new Error('element not found');
                }

                let indexForNewElement = overElementIndex; // i assume i'm on top-half
                if (isDroppingOverDesignerElementBottomHalf) {
                    indexForNewElement = overElementIndex + 1;
                }

                return addField(indexForNewElement, newElement);
            }

            const isDraggingDesignerElement =
                active.data?.current?.isDesignerElement;

            if (isDraggingDesignerElement) {
                const activeIdx = fields.findIndex(
                    (field) => field.id === active.data.current?.elementId,
                );
                const overIdx = fields.findIndex(
                    (field) => field.id === over.data.current?.elementId,
                );
                setFields(arrayMove(fields, activeIdx, overIdx));
            }
        },
    });

    return (
        <div ref={setNodeRef} className='relative overflow-hidden'>
            <div className='hide-scrollbar relative h-full space-y-2 overflow-y-auto overflow-x-hidden bg-[#f4f4f4] p-8 shadow-lg'>
                <SortableContext items={fields}>
                    {fields
                        .filter((field) => field.page === current_page)
                        .map((field) => (
                            <DesignerElementWrapper
                                key={field.id}
                                element={field}
                            />
                        ))}
                </SortableContext>
                {isOver && (
                    <div className='grid h-32 place-items-center rounded-lg border-2 border-dashed border-metalic-blue text-metalic-blue'>
                        Drag and drop elements from the left to add a new
                        component
                    </div>
                )}
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

    const sortable = useSortable({
        id: element.id + '-sortable-handler',
        data: {
            type: element.type,
            elementId: element.id,
            isDesignerElement: true,
        },
    });

    if (sortable.isDragging) return null;

    const DesignerElement = FormElements[element.type].designerComponent;

    return (
        <>
            {topHalf.isOver && (
                <div className='grid h-32 place-items-center rounded-lg border-2 border-dashed border-metalic-blue text-metalic-blue'>
                    Drag and drop elements from the left to add a new component
                </div>
            )}
            <div
                ref={sortable.setNodeRef}
                {...sortable.listeners}
                {...sortable.attributes}
                className='relative'
            >
                <div
                    ref={topHalf.setNodeRef}
                    className='pointer-events-none absolute h-1/2 w-full rounded-t-md'
                />
                <div
                    ref={bottomHalf.setNodeRef}
                    className='pointer-events-none  absolute bottom-0 h-1/2 w-full rounded-b-md'
                />
                <DesignerElement element={element} />
            </div>
            {bottomHalf.isOver && (
                <div className='grid h-32 place-items-center rounded-lg border-2 border-dashed border-metalic-blue text-metalic-blue'>
                    Drag and drop elements from the left to add a new component
                </div>
            )}
        </>
    );
}

export default Dropzone;
