import { useState } from 'react';
import { Active, DragOverlay, useDndMonitor } from '@dnd-kit/core';
import { FormElements } from './FormElement';
import { GripVerticalIcon } from 'lucide-react';
import ToolboxItem from './Toolbox/ToolBoxItem';
import useFormBuilder from '@/hooks/useFormBuilder';

function OverlayWrapper() {
    const { pages, current_page_id } = useFormBuilder();
    const [draggedItem, setDraggedItem] = useState<Active | null>(null);
    const currentPage = pages.find((page) => page.page_id === current_page_id);

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
    if (isDesignerElement && currentPage) {
        const elementId = draggedItem.data?.current?.elementId;
        const element = currentPage.fields.find((el) => el.id === elementId);
        if (!element) node = <div>Element not found!</div>;
        else {
            const DesignerElementComponent =
                FormElements[element.type].designerComponent;
            node = (
                <div className='pointer-events-none relative flex items-center gap-4 rounded-lg border bg-white p-4 opacity-50'>
                    <GripVerticalIcon />
                    <DesignerElementComponent element={element} />
                </div>
            );
        }
    }

    return <DragOverlay>{node}</DragOverlay>;
}

export default OverlayWrapper;
