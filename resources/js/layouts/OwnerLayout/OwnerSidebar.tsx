import { sidebarItems } from './sidebar-items';

function OwnerSidebar() {
    // TODO use recursive approach
    // concatenate the paths

    return (
        <div>
            {sidebarItems.map((sidebarItem) => {
                if (sidebarItem.sublinks && sidebarItem.sublinks?.length > 0)
                    return 'Nested Links';
                return 'Single Link';
            })}
        </div>
    );
}

export default OwnerSidebar;
