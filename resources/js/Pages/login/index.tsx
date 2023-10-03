import { Button } from "@/Components/ui/button";
import { Link } from "@inertiajs/react";

function LoginPage() {
    return (
        <div className="p-4">
            <Link href="/admin?active=Dashboard">
                <Button>Goto Admin</Button>
            </Link>
        </div>
    );
}

export default LoginPage;
