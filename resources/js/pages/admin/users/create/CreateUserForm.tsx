import * as z from "zod";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";

const formSchema = z.object({
    first_name: z.string().nonempty(),
    last_name: z.string().nonempty(),
    phone: z.string().min(11).max(11),
    email: z.string().email(),
});

function CreateUserForm() {
    const form = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            first_name: "",
            last_name: "",
            phone: "",
            email: "",
        },
    });

    const onSubmit = (values: z.infer<typeof formSchema>) => {
        //  this handles the user creation
    };

    return (
        <Form {...form}>
            <form
                onSubmit={form.handleSubmit(onSubmit)}
                className="space-y-8 relative"
            >
                <div className="grid gap-4">
                    <h1 className="text-headline-4 font-semibold text-gray-400">
                        Personal Information
                    </h1>
                    <div className="grid grid-cols-2 gap-4 items-start">
                        <FormField
                            control={form.control}
                            name="first_name"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>First Name</FormLabel>
                                    <FormControl>
                                        <Input
                                            className="p-6 pr-16 bg-light-carbon placeholder:text-gray-400 rounded-lg"
                                            {...field}
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />
                        <FormField
                            control={form.control}
                            name="last_name"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>Last Name</FormLabel>
                                    <FormControl>
                                        <Input
                                            className="p-6 pr-16 bg-light-carbon placeholder:text-gray-400 rounded-lg"
                                            {...field}
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />
                    </div>
                    <h1 className="text-headline-4 font-semibold text-gray-400">
                        Personal Information
                    </h1>
                    <div className="grid grid-cols-2 gap-4 items-start">
                        <FormField
                            control={form.control}
                            name="phone"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>Phone</FormLabel>
                                    <FormControl>
                                        <Input
                                            className="p-6 pr-16 bg-light-carbon placeholder:text-gray-400 rounded-lg"
                                            {...field}
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />
                        <FormField
                            control={form.control}
                            name="email"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>Email</FormLabel>
                                    <FormControl>
                                        <Input
                                            className="p-6 pr-16 bg-light-carbon placeholder:text-gray-400 rounded-lg"
                                            {...field}
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />
                    </div>
                </div>
                <div className="flex justify-end">
                    <Button
                        type="submit"
                        className="bg-metalic-blue uppercase hover:bg-metalic-blue/90 px-20 py-6"
                    >
                        create
                    </Button>
                </div>
            </form>
        </Form>
    );
}

export default CreateUserForm;
