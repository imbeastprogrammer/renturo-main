import { PropsWithChildren } from 'react';
import { FaSearch } from 'react-icons/fa';
import { IoClose } from 'react-icons/io5';
import { Input } from '@/components/ui/input';

function FormLocationPicker() {
    return (
        <div className='space-y-4'>
            <Searchbar />
            <div className='flex gap-2'>
                <SelectedItem onRemove={() => alert('remove button clicked')}>
                    Intramuros, Manila + 25 mi
                </SelectedItem>
                <SelectedItem onRemove={() => alert('remove button clicked')}>
                    Intramuros, Manila + 25 mi
                </SelectedItem>
            </div>
            <Map />
        </div>
    );
}

function Searchbar() {
    return (
        <div className='relative flex items-center'>
            <FaSearch className='absolute left-2 text-black/50' />
            <Input
                className='rounded-none border-0 border-b pl-10 text-base placeholder:text-black/50 focus-visible:ring-transparent'
                placeholder='Search Locations'
            />
        </div>
    );
}

type SelectedItemProps = PropsWithChildren & { onRemove: () => void };
function SelectedItem({ children, onRemove }: SelectedItemProps) {
    return (
        <div className='flex items-center gap-2 rounded-lg bg-metalic-blue/5 p-2 px-4 text-metalic-blue/70'>
            {children}
            <button onClick={onRemove}>
                <IoClose />
            </button>
        </div>
    );
}

function Map() {
    return <div className='h-[300px] rounded-lg bg-metalic-blue/20'></div>;
}

export default FormLocationPicker;
