import AdminLayout from "@/layouts/AdminLayout";
import CreateUserForm from "./CreateUserForm";

function CreateUserPage() {
    return (
        <AdminLayout>
            <div className="p-8 h-full grid grid-rows-[auto_auto_1fr] gap-y-4">
                <p className="text-gray-500 font-medium">
                    Users / User Management / Add User
                </p>
                <h1 className="text-headline-3 leading-none font-semibold">
                    Add User
                </h1>
                <CreateUserForm />
            </div>
        </AdminLayout>
    );
}

export default CreateUserPage;
