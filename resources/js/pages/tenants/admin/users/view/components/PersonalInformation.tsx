import * as z from 'zod';
import { useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Switch } from '@/components/ui/switch';
import { Form } from '@/components/ui/form';
import { Button } from '@/components/ui/button';
import FormInput from '@/components/forms/FormInput';

const formSchema = z.object({
    first_name: z.string(),
    last_name: z.string(),
    gender: z.string(),
    email: z.string(),
    phone_number: z.string(),
});

function PersonalIformation() {
    const [allowEditing, setAllowEditing] = useState(false);

    const form = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            first_name: '',
            last_name: '',
            gender: '',
            phone_number: '',
            email: '',
        },
    });

    const onSubmit = (values: z.infer<typeof formSchema>) => {
        // submission here
    };

    return (
        <Form {...form}>
            <form onSubmit={form.handleSubmit(onSubmit)}>
                <div className='grid gap-6'>
                    <div className='space-y-4'>
                        <div className='flex items-center justify-between gap-4'>
                            <h1 className='text-[22px] text-heavy-carbon'>
                                Personal Information
                            </h1>
                            <label className='flex items-center gap-4 rounded-full bg-gray-200 p-2 px-4 font-semibold'>
                                <Switch
                                    checked={allowEditing}
                                    onCheckedChange={() =>
                                        setAllowEditing(!allowEditing)
                                    }
                                />
                                Allow Edit
                            </label>
                        </div>
                        <div className='grid grid-cols-2 gap-4'>
                            <FormInput
                                label='First Name'
                                name='first_name'
                                disabled={!allowEditing}
                                control={form.control}
                            />
                            <FormInput
                                label='Last Name'
                                name='last_name'
                                disabled={!allowEditing}
                                control={form.control}
                            />
                            <FormInput
                                label='Gender'
                                name='gender'
                                disabled={!allowEditing}
                                control={form.control}
                            />
                        </div>
                    </div>
                    <div className='space-y-4'>
                        <h1 className='text-[22px] text-heavy-carbon'>
                            Contact Information
                        </h1>
                        <div className='grid grid-cols-2 gap-4'>
                            <FormInput
                                label='Phone Number'
                                name='phone_number'
                                disabled={!allowEditing}
                                control={form.control}
                            />
                            <FormInput
                                label='Email Address'
                                name='email'
                                disabled={!allowEditing}
                                control={form.control}
                            />
                        </div>
                    </div>
                    {allowEditing && (
                        <Button
                            type='submit'
                            className='ml-auto w-max bg-metalic-blue p-6 px-20 uppercase hover:bg-metalic-blue/90'
                        >
                            Update
                        </Button>
                    )}
                </div>
            </form>
        </Form>
    );
}

export default PersonalIformation;
