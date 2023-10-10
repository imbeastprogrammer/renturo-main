import * as z from 'zod';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
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
                        <h1 className='text-[22px] text-heavy-carbon'>
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
                                control={form.control}
                            />
                            <FormInput
                                label='Email Address'
                                name='email'
                                control={form.control}
                            />
                        </div>
                    </div>
                    <Button
                        type='submit'
                        className='ml-auto w-max bg-metalic-blue p-6 px-20 uppercase hover:bg-metalic-blue/90'
                    >
                        Update
                    </Button>
                </div>
            </form>
        </Form>
    );
}

export default PersonalIformation;
