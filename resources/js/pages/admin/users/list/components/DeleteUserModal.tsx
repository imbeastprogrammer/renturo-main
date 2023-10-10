import { Button } from "@/components/ui/button";
import {
    AlertDialog,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from "@/components/ui/alert-dialog";

type DeleteUserModalProps = {
    userToDeleteId: number;
    isOpen: boolean;
    onClose: () => void;
};

function DeleteUserModal({ isOpen, onClose }: DeleteUserModalProps) {
    const handleOpenChange = (open: boolean) => onClose();

    return (
        <AlertDialog open={isOpen} onOpenChange={handleOpenChange}>
            <AlertDialogContent className="max-w-md p-8">
                <AlertDialogHeader>
                    <AlertDialogTitle>
                        Are you sure you want to delete this user?
                    </AlertDialogTitle>
                    <AlertDialogDescription>
                        Do you really want to delete this user? Action can be
                        undone later.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <Button
                        onClick={onClose}
                        className="w-full uppercase"
                        variant="outline"
                    >
                        cancel
                    </Button>
                    <Button className="w-full uppercase" variant="destructive">
                        yes
                    </Button>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}

export default DeleteUserModal;
