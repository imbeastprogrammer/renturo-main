import { Button } from "@/components/ui/button";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { LucideIcon, MoreVerticalIcon } from "lucide-react";

type MenuItem = { label: string; icon: LucideIcon };

type ActionMenuProps = {
    menuItems: MenuItem[];
    onSelect: (value: string) => void;
};

function ActionMenu({ menuItems, onSelect }: ActionMenuProps) {
    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button variant="outline" size="icon">
                    <MoreVerticalIcon className="w-4 h-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent className="-translate-x-10">
                {menuItems.map((menuItem) => (
                    <DropdownMenuItem
                        key={menuItem.label}
                        className="flex gap-2 items-center"
                        onClick={() => onSelect(menuItem.label)}
                    >
                        <menuItem.icon className="h-4 w-4" /> {menuItem.label}
                    </DropdownMenuItem>
                ))}
            </DropdownMenuContent>
        </DropdownMenu>
    );
}

export default ActionMenu;
