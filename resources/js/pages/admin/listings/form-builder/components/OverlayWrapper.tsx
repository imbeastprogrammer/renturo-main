import { useState } from 'react';
import { Active, DragOverlay, useDndMonitor } from '@dnd-kit/core';
import { FormElements } from './FormElement';
import ToolboxItem from './Toolbox/ToolBoxItem';
import useFormBuilder from '@/hooks/useFormBuilder';

function OverlayWrapper() {
    const { fields } = useFormBuilder();
    const [draggedItem, setDraggedItem] = useState<Active | null>(null);

    useDndMonitor({
        onDragStart: (event) => {
            setDraggedItem(event.active);
        },
        onDragCancel: () => {
            setDraggedItem(null);
        },
        onDragEnd: () => {
            setDraggedItem(null);
        },
    });

    if (!draggedItem) return null;

    let node = <div>No drag overlay</div>;
    const isToolboxItem = draggedItem.data?.current?.isToolboxItem;

    if (isToolboxItem) {
        const activeToolboxiItem = draggedItem.data.current?.toolboxItem;
        node = <ToolboxItem {...activeToolboxiItem} />;
    }

    const isDesignerElement = draggedItem.data?.current?.isDesignerElement;
    if (isDesignerElement) {
        const elementId = draggedItem.data?.current?.elementId;
        const element = fields.find((el) => el.id === elementId);
        if (!element) node = <div>Element not found!</div>;
        else {
            const DesignerElementComponent =
                FormElements[element.type].designerComponent;
            node = (
                <div className='pointer-events-none opacity-80'>
                    <DesignerElementComponent element={element} />
                </div>
            );
        }
    }

    return <DragOverlay>{node}</DragOverlay>;
}

export default OverlayWrapper;
