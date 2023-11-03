import * as z from 'zod';
import { router } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Form } from '@/components/ui/form';
import { Button } from '@/components/ui/button';
import FormInput from '@/components/forms/FormInput';
import FormSelect from '@/components/forms/FormSelect';

const formSchema = z.object({
    first_name: z.string().nonempty(),
    last_name: z.string().nonempty(),
    phone: z.string().min(11).max(11),
    email: z.string().email(),
    role: z.string().nonempty(),
});

function CreateUserForm() {
    const form = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            first_name: '',
            last_name: '',
            phone: '',
            email: '',
            role: '',
        },
    });

    const onSubmit = (values: z.infer<typeof formSchema>) => {
        router.post('/admin/users', { ...values, mobile_no: values.phone });
    };

    return (
        <Form {...form}>
            <form
                onSubmit={form.handleSubmit(onSubmit)}
                className='relative space-y-8'
            >
                <div className='grid gap-4'>
                    <h1 className='text-headline-4 font-semibold text-gray-400'>
                        Personal Information
                    </h1>
                    <div className='grid grid-cols-2 items-start gap-4'>
                        <FormInput
                            name='first_name'
                            label='First Name'
                            control={form.control}
                        />
                        <FormInput
                            name='last_name'
                            label='Last Name'
                            control={form.control}
                        />
                    </div>
                    <h1 className='text-headline-4 font-semibold text-gray-400'>
                        Personal Information
                    </h1>
                    <div className='grid grid-cols-2 items-start gap-4'>
                        <FormInput
                            name='phone'
                            label='Phone'
                            control={form.control}
                        />
                        <FormInput
                            name='email'
                            label='Email'
                            control={form.control}
                        />

                        <FormSelect
                            name='role'
                            label='Role'
                            data={[
                                { label: 'ADMIN', value: 'ADMIN' },
                                { label: 'OWNER', value: 'OWNER' },
                                { label: 'USER', value: 'USER' },
                            ]}
                            control={form.control}
                        />
                    </div>
                </div>
                <div className='flex justify-end'>
                    <Button
                        type='submit'
                        className='bg-metalic-blue px-20 py-6 uppercase hover:bg-metalic-blue/90'
                    >
                        create
                    </Button>
                </div>
            </form>
        </Form>
    );
}

export default CreateUserForm;
