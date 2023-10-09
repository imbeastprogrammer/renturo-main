import Searchbar from "@/components/Searchbar";

function AdminLayoutHeader() {
    return (
        <header className="p-4">
            <nav>
                <Searchbar />
            </nav>
        </header>
    );
}

export default AdminLayoutHeader;
