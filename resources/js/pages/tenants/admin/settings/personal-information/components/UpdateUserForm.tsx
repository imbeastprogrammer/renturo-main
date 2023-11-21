import * as z from 'zod';
import { router } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Form } from '@/components/ui/form';
import FormInput from '@/components/forms/FormInput';
import { Button } from '@/components/ui/button';
import FormPhoneNumberInput from '@/components/forms/FormPhoneNumberInput';

const formSchema = z.object({
    first_name: z.string(),
    last_name: z.string(),
    gender: z.string(),
    martial_status: z.string(),
    address_line1: z.string(),
    address_line2: z.string(),
    country: z.string(),
    province: z.string(),
    city: z.string(),
    zipcode: z.string(),
    email: z.string(),
    phone_number: z.string(),
});

function UpdateUserForm() {
    const form = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            first_name: '',
            last_name: '',
            gender: '',
            martial_status: '',
            address_line1: '',
            address_line2: '',
            country: '',
            province: '',
            city: '',
            zipcode: '',
            phone_number: '',
            email: '',
        },
    });

    const onSubmit = (values: z.infer<typeof formSchema>) => {
        router.visit('/admin/settings/personal-information', { replace: true });
    };

    console.log(form.watch('phone_number'));

    return (
        <Form {...form}>
            <form onSubmit={form.handleSubmit(onSubmit)}>
                <div className='grid gap-8 p-6'>
                    <div className='space-y-4'>
                        <h1 className='text-[22px] text-black/30'>
                            Personal Information
                        </h1>
                        <div className='grid grid-cols-2 gap-4'>
                            <FormInput
                                label='First Name'
                                name='first_name'
                                control={form.control}
                            />
                            <FormInput
                                label='Last Name'
                                name='last_name'
                                control={form.control}
                            />
                            <FormInput
                                label='Gender'
                                name='gender'
                                control={form.control}
                            />
                            <FormInput
                                label='Marital Status'
                                name='marital_status'
                                control={form.control}
                            />
                        </div>
                    </div>
                    <div className='space-y-4'>
                        <h1 className='text-[22px] text-black/30'>
                            Address Information
                        </h1>
                        <div className='grid grid-cols-2 gap-4'>
                            <FormInput
                                label='Address Line 1'
                                name='address_line1'
                                control={form.control}
                            />
                            <FormInput
                                label='Address Line 2'
                                name='address_line2'
                                control={form.control}
                            />
                            <FormInput
                                label='Country'
                                name='country'
                                control={form.control}
                            />
                            <FormInput
                                label='State/Province'
                                name='province'
                                control={form.control}
                            />
                            <FormInput
                                label='City'
                                name='city'
                                control={form.control}
                            />
                            <FormInput
                                label='Zip Code'
                                name='zipcode'
                                control={form.control}
                            />
                        </div>
                    </div>
                    <div className='space-y-4'>
                        <h1 className='text-[22px] text-black/30'>
                            Contact Information
                        </h1>
                        <div className='grid grid-cols-2 gap-4'>
                            <FormPhoneNumberInput
                                label='Phone Number'
                                name='phone_number'
                                control={form.control}
                            />
                            <FormInput
                                label='Email Address'
                                name='email'
                                control={form.control}
                            />
                        </div>
                    </div>
                    <div className='flex justify-end'>
                        <Button
                            type='submit'
                            className='bg-metalic-blue px-8 text-xl font-bold hover:bg-metalic-blue/90'
                        >
                            Save
                        </Button>
                    </div>
                </div>
            </form>
        </Form>
    );
}

export default UpdateUserForm;
