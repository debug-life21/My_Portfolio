const custom={
    "name":"wendmu",
    "age":"20",
    "email":"debugelife@gmail.com",
    "expriance":`{"html":"5 years","css":"5 years","javascript":"3 years"}`,
    "hobby":["coding","music","travel"],
};
console.log(custom);
let jsonstringify=JSON.stringify(custom);
console.log(jsonstringify);
let jsonparse=JSON.parse(jsonstringify);
console.log(jsonparse);
//date
const date=new Date();
const year=date.getFullYear();
const month = date.getMonth();
const day=date.getDay();
const hour=date.getHours();
console.log(`year=${year}`);
console.log(`month=${month}`);
console.log(hour);
console.log(date.toLocaleString());
//setinterval 
const intervalid=setInterval(
() => console.log(`this function excuted every 10 seconds.`)
,10000 );
//stop tyhe intyerval after some seconds
setTimeout(function(){clearInterval(intervalid)
console.log("stop the interval after 5 times excuted")},50000)
//settimeout
setTimeout(()=> console.log(`this function excuted after 5 seconds`)
,5000);
//templet stringes also known as template literals are 
// a feature in javascript allows you to craete strings with 
// embedded expression. denoted by backticks ` ` insteade of '' and "";
// template strings provide amore flexible and concise  way to 
// construct strings.espacially involve variables and exprassions
let name="wendmu";
console.log(`theis is 
    some number :${34+89} 
    my name is:${name}`);
    //ein exprassion
   function info(){return " my name is wendmu tsegaye now web development student." };
        console.log(`${info()}`)
// arrow function , also known as fat arrow function , 
//it is aconcise and shorter way to define functions  in JS
let great= (username) => {
 return `hello ${username}`};
    console.log(great('wendmu')); //other example
    double = n => n*2;
    console.log(double(109));
//Enhanced object literals ,this enhancement make it convenient and concise 
//to define object properties and methods
function user(Name,age,work){
    return{ Name,age,work , 
        intro:()=>{console.log(`my name is ${Name} & I am ${age} 
            years old & I'm a ${work}`)}//use enhanced object
    };
}
const wend=user("wendmu",20,"developer");
console.log(wend);
console.log(wend.intro());
//defualt function paramaters
function well(user="wendmu"){
    return user
};
console.log(well());
//spread operators
function giveme4(a,b,c,d){
    console.log("a",a)
    console.log("b",b)
    console.log("c",c)
    console.log("d",d)
}
const colors =["red","blue","green","yellow"]
giveme4(1,2,3,4);
giveme4(...colors);//this is spread operator use in function
//also use in array and objects
const peoples=["alex","abebe","worku"];
const people=['abdu'];
const addarray=[...people,...peoples]
console.log(addarray)
console.log('wendmu','aster',...peoples,'john')
const person={
    name:"john",
    age:65,
    gender:'male',
};
const clone={...person, work:"student" ,location:"debark"}
console.log(clone);
//rest paramater