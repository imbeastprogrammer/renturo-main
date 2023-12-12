import { FaSearch } from 'react-icons/fa';
import { ComponentPropsWithoutRef } from 'react';

type SearchbarProps = ComponentPropsWithoutRef<'input'>;
function Searchbar(props: SearchbarProps) {
    return (
        <div className='flex h-[40px] w-[300px] items-center gap-4 rounded-full bg-white px-4'>
            <FaSearch className='h-[20px] w-[20px] text-black/30' />
            <input
                type='text'
                className='text-xl font-semibold outline-none placeholder:text-black/30'
                {...props}
            />
        </div>
    );
}

export default Searchbar;
