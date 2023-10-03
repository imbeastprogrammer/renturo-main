import route from "ziggy-js";
import { Link, InertiaLinkProps } from "@inertiajs/react";

import {
    HomeIcon,
    LogOutIcon,
    LucideIcon,
    PlusIcon,
    SettingsIcon,
    UsersIcon,
} from "lucide-react";
import { cn } from "@/lib/utils";
import dashboardLogo from "@/assets/dashboard-logo.png";

const dashboardLinks = [
    { label: "Dashboard", to: "admin", icon: HomeIcon, links: [] },
    {
        label: "Post",
        to: "admin.post",
        icon: PlusIcon,
        links: [
            { label: "Listings", to: "admin.post" },
            { label: "Bookings", to: "admin.post.bookings" },
            { label: "Categories", to: "admin.post.categories" },
        ],
    },
    { label: "Users", to: "admin.users", icon: UsersIcon, links: [] },
    {
        label: "Settings",
        to: "admin.settings",
        icon: SettingsIcon,
        links: [],
    },
];

type SidebarLinkProps = InertiaLinkProps & {
    icon: LucideIcon;
    label: string;
    isActive: boolean;
};

function SidebarLink({ isActive, ...props }: SidebarLinkProps) {
    return (
        <Link
            {...props}
            className={cn(
                "relative inline-grid w-full place-items-center gap-2 rounded-l-full p-2 text-[15px] transition",
                { "text-metalic-blue bg-white": isActive }
            )}
        >
            {isActive && (
                <span className="absolute -top-5 left-0 h-5 w-full bg-white">
                    <div className="bg-metalic-blue absolute inset-0 rounded-br-full"></div>
                </span>
            )}
            <props.icon className="h-[43px] w-[43px]" />
            {props.label}
            {isActive && (
                <span className="absolute -bottom-5 left-0 h-5 w-full bg-white">
                    <div className="bg-metalic-blue absolute inset-0 rounded-tr-full"></div>
                </span>
            )}
        </Link>
    );
}

function SecondaryLink({
    isActive,
    ...props
}: Omit<SidebarLinkProps, "icon" | "label">) {
    return (
        <Link
            {...props}
            className={cn("inline-block rounded-lg p-2 px-4 transition", {
                "bg-gray-100": isActive,
            })}
        >
            {props.children}
        </Link>
    );
}

function AdminSidebar() {
    const searchParams = new URLSearchParams(window.location.search);
    const activeLink = searchParams.get("active");

    const activeLinkChildrenLinks = dashboardLinks.find(
        (link) => link.label === activeLink
    );

    return (
        <aside className="h-full p-4">
            <div className="flex h-full rounded-lg border shadow-lg">
                <div className="bg-metalic-blue grid h-full w-[130px] grid-rows-[1fr_auto] rounded-lg px-4 py-8 pr-0 text-white">
                    <div>
                        <img
                            className="h-[80px]a mx-auto w-[80px] object-contain"
                            src={dashboardLogo}
                        />
                        <nav>
                            <ul className="mt-6 space-y-4">
                                {dashboardLinks.map((link, i) => (
                                    <li key={i}>
                                        <SidebarLink
                                            icon={link.icon}
                                            href={route(link.to, {
                                                active: link.label,
                                            })}
                                            label={link.label}
                                            isActive={activeLink === link.label}
                                        />
                                    </li>
                                ))}
                            </ul>
                        </nav>
                    </div>
                    <LogOutIcon className="mx-auto h-[40px] w-[40px]" />
                </div>
                {activeLinkChildrenLinks &&
                    activeLinkChildrenLinks.links.length > 0 && (
                        <nav className="flex w-[200px] flex-col gap-4 p-4">
                            {activeLinkChildrenLinks?.links.map((link) => (
                                <SecondaryLink
                                    isActive={route().current(link.to)}
                                    key={link.to}
                                    href={route(link.to, {
                                        active: activeLink as string,
                                    })}
                                >
                                    {link.label}
                                </SecondaryLink>
                            ))}
                        </nav>
                    )}
            </div>
        </aside>
    );
}

export default AdminSidebar;
