console.time("time");

function a(n)
{
    let count = 0;
    for (let i = 0; i < 30*n; i++) {
        count+=1;
    }

    console.log(count);
    return count;
}

function a(n)
{
    let count = 0;
    for (let i = 0; i < 50*n; i++) {
        count+=1;
    }
console.log(count);
return count;
}

a(5);
console.timeEnd('time');


