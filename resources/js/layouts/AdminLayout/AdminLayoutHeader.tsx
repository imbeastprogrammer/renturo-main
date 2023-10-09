import Searchbar from "@/components/Searchbar";
import UserButton from "@/components/UserButton";

function AdminLayoutHeader() {
    return (
        <header>
            <div className="flex gap-4 items-center justify-between">
                <div className="flex-1 max-w-xl">
                    <Searchbar />
                </div>
                <UserButton />
            </div>
        </header>
    );
}

export default AdminLayoutHeader;
